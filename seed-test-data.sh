#!/bin/bash
# Test Data Seeding Script for Support Desk

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║  Support Desk - Test Data Seeding Script                       ║"
echo "╚════════════════════════════════════════════════════════════════╝"

cd d:\Richard.WWW\support_desk-main

echo ""
echo "📊 This will seed the database with:"
echo "  • 8 Departments"
echo "  • 1200+ Users (all with password: 123)"
echo "  • 20+ Clients"
echo "  • 9000+ Tickets (6 months of data)"
echo "  • 30000+ Comments"
echo "  • Attachments, History, Leave Requests, Audit Trails"
echo ""
echo "⏱️  Estimated time: 2-5 minutes"
echo ""

# Check if fresh install
echo "Clearing any existing data..."
php artisan db:wipe --no-interaction 2>/dev/null || echo "Database was empty"

echo ""
echo "🔄 Running migrations..."
php artisan migrate --seed

echo ""
echo "✅ Seeding completed!"
echo ""
echo "🔑 Test Credentials:"
echo "  Username: operator_0001"
echo "  Password: 123"
echo ""
echo "🌐 Access the application:"
echo "  http://localhost:8000"
echo ""
