<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function index()
    {
        return view('ai.assistant');
    }

    public function suggest(Request $request)
    {
        $request->validate([
            'prompt' => 'nullable|string|max:1000'
        ]);

        $prompt = trim($request->input('prompt', ''));

        // gather a few quick stats to make suggestions contextual
        try {
            $open = Ticket::where('status', 'open')->count();
            $overdue = Ticket::whereNotNull('due_at')->where('due_at', '<', now())->count();
            $unassigned = Ticket::whereNull('assigned_to')->count();
        } catch (\Throwable $e) {
            $open = $overdue = $unassigned = null;
        }

        // If an external AI endpoint is configured, call it. Otherwise return internal heuristics.
        $externalUrl = config('services.ai_assistant.url') ?: env('AI_ASSISTANT_ENDPOINT');
        $externalKey = config('services.ai_assistant.key') ?: env('AI_ASSISTANT_KEY');

        if ($externalUrl) {
            try {
                $payload = [
                    'prompt' => $prompt,
                    'context' => [
                        'open' => $open,
                        'overdue' => $overdue,
                        'unassigned' => $unassigned,
                    ],
                ];

                $client = Http::timeout(8)->retry(1, 250);
                if ($externalKey) {
                    $client = $client->withToken($externalKey);
                }

                $res = $client->post($externalUrl, $payload);
                if ($res->successful()) {
                    $body = $res->json();
                    if (isset($body['suggestions']) && is_array($body['suggestions'])) {
                        return response()->json(['ok' => true, 'prompt' => $prompt, 'suggestions' => $body['suggestions']]);
                    }
                    if (isset($body['choices']) && is_array($body['choices'])) {
                        // minimal normalization for common AI responses
                        $suggestions = array_map(function ($c) {
                            return is_array($c) && isset($c['text']) ? $c['text'] : (string) $c;
                        }, $body['choices']);
                        return response()->json(['ok' => true, 'prompt' => $prompt, 'suggestions' => $suggestions]);
                    }
                }
            } catch (\Throwable $e) {
                // Fall through to local heuristics below
            }
        }

        $base = [];
        if ($overdue !== null && $overdue > 0) {
            $base[] = "There are {$overdue} overdue ticket(s). Prioritise these and assign owners immediately.";
        }
        if ($unassigned !== null && $unassigned > 0) {
            $base[] = "{$unassigned} tickets are unassigned. Consider auto-assign rules or quick triage to distribute workload.";
        }
        if ($open !== null) {
            $base[] = "Open tickets: {$open}. Focus on high-severity ones first and batch similar issues.";
        }

        $suggestion = [];
        if ($prompt !== '') {
            $suggestion[] = "For: \"{$prompt}\" — I recommend prioritising by SLA, assigning an owner, and adding a clear next action.";
        }

        $suggestion = array_merge($suggestion, $base);

        if (empty($suggestion)) {
            $suggestion[] = "No actionable data available. Ensure the system has tickets or check permissions.";
        }

        return response()->json([
            'ok' => true,
            'prompt' => $prompt,
            'suggestions' => $suggestion
        ]);
    }
}
