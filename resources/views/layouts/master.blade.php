<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Flaxem Support Desk</title>
    <link rel="icon" type="image/x-icon" href="{{URL::asset('assets/images/centenary-small.png')}}">

    @include('layouts.head-css')
    <style>
        :root {
            --brand-blue: #3496D7;
            --brand-light: #e8f3ff;
            --text-muted: rgba(0,0,0,0.6);
            --card-radius: 10px;
            --base-font-size: 13px;
        }

        html, body { font-size: var(--base-font-size); }

        body {
            padding-top: 0;
        }

        #global-loader {
            position: absolute;
            inset: 0;
            background: transparent;
            z-index: 50;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        #global-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader-simple {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 3px solid rgba(52, 150, 215, 0.15);
            border-top-color: #3496D7;
            animation: spin 0.8s linear infinite;
            box-shadow: 0 0 0 1px rgba(52, 150, 215, 0.06);
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ── Print / Download styles ─────────────────────────── */
        @media print {
            /* Hide chrome */
            #global-loader,
            .navbar,
            .sidebar-main,
            .page-footer,
            footer,
            .right-sidebar,
            .breadcrumb-print-hide,
            .no-print {
                display: none !important;
            }
            /* Remove sidebar offset so content fills the page */
            .page-content  { padding-top: 0 !important; }
            .content-wrapper { margin-left: 0 !important; width: 100% !important; }
            .content-inner { padding: 0 !important; }
            /* Force colour backgrounds / badges to print */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            /* Expand printable area full width */
            #printable-content { width: 100% !important; }
            /* Avoid page breaks inside cards */
            .card, .receipt-document { break-inside: avoid; }
            .table { font-size: 10px !important; }
        }
        /* Make cards and typography slightly smaller and tighter for a professional compact layout */
        .card { border-radius: var(--card-radius); }

        .receipt-document {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(52, 150, 215, 0.12);
            border-radius: 22px;
            padding: 1.25rem;
            box-shadow: 0 18px 40px rgba(52, 150, 215, 0.08);
        }

        .receipt-header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 1px dashed rgba(15, 76, 184, 0.2);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .receipt-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            background: #3496D7;
            color: #fff;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .receipt-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--brand-blue);
            margin-bottom: 0.25rem;
        }

        .receipt-subtitle {
            color: #52607a;
            margin-bottom: 0;
        }

        .receipt-idbox {
            min-width: 210px;
            background: rgba(52, 150, 215, 0.04);
            border-radius: 18px;
            padding: 0.85rem 1rem;
            border: 1px solid rgba(52, 150, 215, 0.08);
        }

        .receipt-idbox .receipt-meta-label,
        .receipt-meta-card .receipt-meta-label {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.3rem;
        }

        .receipt-idbox .receipt-meta-value,
        .receipt-meta-card .receipt-meta-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.98rem;
        }

        .receipt-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.75rem;
        }

        .receipt-meta-card {
            background: #fff;
            border: 1px solid rgba(52, 150, 215, 0.1);
            border-radius: 16px;
            padding: 0.85rem;
        }

        .receipt-section {
            margin-top: 1rem;
        }

        .receipt-section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.6rem;
        }

        .receipt-audit-table th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #52607a;
        }

        .receipt-footer {
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #64748b;
            text-align: right;
        }
        .card .card-body { padding: 0.85rem !important; }
        .page-content { font-size: 0.9rem; padding-top: 0; }
        .content-wrapper { padding: 0.55rem; padding-top: 0.7rem; position: relative; }
        .sidebar .nav-link { font-size: 0.88rem; padding: 0.45rem 0.75rem; }
        .sidebar .nav-item-header .text-uppercase { font-size: 0.68rem; }
        .sidebar.sidebar-dark { box-shadow: 2px 0 16px rgba(0,0,0,0.12); }
        .page-content { display: flex; }
        .navbar { border-bottom: 3px solid #dc3545 !important; }
        .topbar { border-bottom: 1px solid #dc3545 !important; min-height: 44px !important; }
        .topbar img { height: 28px !important; }
        .topbar span[style*="font-size:1.1rem"] { font-size: 1rem !important; }
        .topbar #topbar-time { font-size: 0.9rem !important; }
        .container.py-5 { padding-top: 2rem; padding-bottom: 2rem; }
        .sidebar .nav-group-sub.show,
        .sidebar .nav-item-open > .nav-group-sub {
            display: block !important;
            height: auto !important;
            visibility: visible !important;
            overflow: visible !important;
        }

        .sidebar-scroll-hide::-webkit-scrollbar {
            display: none;
        }
    </style>

</head>

<body>
    @auth
    <!-- Top bar like screenshot -->
    <div class="topbar d-flex align-items-center justify-content-between px-4 py-2 border-bottom" style="min-height:48px; background:linear-gradient(90deg, #3496D7 0%, #2475b0 62%, #dc3545 100%); box-shadow:0 2px 12px rgba(52,150,215,0.18);">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ URL::asset('assets/images/centenary.png') }}" alt="Flaxem" style="height:32px; width:auto;">
            <span class="fw-bold" style="color:#fff; font-size:1.1rem; letter-spacing:0.01em;">FLAXEM SUPPORT DESK</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span id="topbar-time" style="font-size:1rem; color:rgba(255,255,255,0.85);"></span>
            <div class="dropdown">
                <button type="button" class="btn btn-link text-white p-0 border-0 d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
                    <i class="ph-user-circle" style="font-size:1.3rem; color:#fff;"></i>
                    <span class="fw-semibold" style="font-size:0.98rem; color:#fff;">{{ auth()->user()->user_surname }} {{ auth()->user()->user_othername }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
    function updateTopbarTime() {
        const el = document.getElementById('topbar-time');
        if (!el) return;

        const now = new Date();
        const formatted = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        });

        el.textContent = formatted;
    }

    setInterval(updateTopbarTime, 1000);
    updateTopbarTime();
    </script>
    @endauth

    <!-- Page content -->
    <div class="page-content">

        @auth
        <!-- sidebar -->
        @include('layouts.sidebar')
        @endauth

        <!-- Main content -->
        <div class="content-wrapper" @guest style="width:100%;" @endguest>
            <!-- GLOBAL LOADER OVERLAY -->
            <div id="global-loader">
                <div class="loader-simple" role="status" aria-hidden="true"></div>
            </div>
            <!-- END GLOBAL LOADER -->

            <!-- Inner content -->
            <div class="content-inner">
                <div id="page-content-shell">
                    @yield('content')
                </div>
            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

    @include('layouts.footer')

    <!-- notification -->
    @include('layouts.notification')

    <!-- right-sidebar content -->
    @include('layouts.right-sidebar')

    <script>
    function showLoader() {
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.classList.remove('hidden');
            loader.style.display = 'flex';
        }
    }

    function hideLoader() {
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.classList.add('hidden');
            // Wait for transition then set display none
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Theme Initialization
        let theme = document.documentElement.getAttribute("data-theme");
        if (theme === "auto") {
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            document.documentElement.setAttribute("data-theme", prefersDark ? "dark" : "light");
        }

        // Show loader on navigation
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                const isAnchor = link.href.includes('#');
                const isToggle = link.hasAttribute('data-bs-toggle');
                const isBlank = link.target === '_blank';
                const isDownload = link.hasAttribute('download') ||
                                   link.href.includes('/download') ||
                                   link.href.includes('export=') ||
                                   link.href.includes('-template');
                const isJavascript = link.href.startsWith('javascript:');
                const isLogoutConfirm = link.onclick && link.onclick.toString().includes('confirmLogout');
                const isSidebarLink = link.closest('.sidebar') && !isAnchor && !isToggle && link.origin === window.location.origin;

                if (isAnchor || isToggle || isBlank || isDownload || isJavascript || isLogoutConfirm || isSidebarLink) {
                    if (isSidebarLink) {
                        return;
                    }
                    return;
                }

                if (link.origin === window.location.origin) {
                    showLoader();
                }
            }
        }, true);

        // Show loader on form submit
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && form.checkValidity()) {
                if(form.classList.contains('no-loader')) return;

                showLoader();

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    const originalContent = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                    setTimeout(() => {
                        if(!submitBtn.disabled) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalContent;
                        }
                    }, 5000);
                }
            }
        }, true);

        // Hide loader when page fully loaded
        window.addEventListener('load', hideLoader);
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) hideLoader();
        });

        // Safety timeout (force hide if something hangs)
        setTimeout(hideLoader, 30000);
    });

    function setActiveSidebarLink(link) {
        document.querySelectorAll('.sidebar .nav-link.active').forEach(item => item.classList.remove('active'));

        if (!link) {
            return;
        }

        link.classList.add('active');

        const parentItem = link.closest('.nav-item-submenu');
        const parentSubmenu = parentItem ? parentItem.querySelector(':scope > .nav-group-sub') : null;

        if (parentItem && parentSubmenu) {
            parentItem.classList.add('nav-item-open');
            parentSubmenu.classList.remove('collapsing');
            parentSubmenu.classList.add('collapse', 'show');
            parentSubmenu.style.display = 'block';
            parentSubmenu.style.height = 'auto';
            parentSubmenu.style.overflow = 'visible';
            parentSubmenu.style.visibility = 'visible';
        }
    }

    function runInlineScripts(container) {
        container.querySelectorAll('script').forEach(script => {
            const nextScript = document.createElement('script');

            Array.from(script.attributes).forEach(attribute => {
                nextScript.setAttribute(attribute.name, attribute.value);
            });

            nextScript.textContent = script.textContent;
            document.body.appendChild(nextScript);
            script.remove();
        });
    }

    async function loadSidebarPage(url, link, pushHistory = true) {
        showLoader();

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load page content');
            }

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nextContent = doc.getElementById('page-content-shell');
            const currentContent = document.getElementById('page-content-shell');

            if (!nextContent || !currentContent) {
                throw new Error('Page content shell not found');
            }

            currentContent.innerHTML = nextContent.innerHTML;
            runInlineScripts(currentContent);
            setActiveSidebarLink(link);
            if (pushHistory) {
                history.pushState({ sidebarUrl: url }, '', url);
            }
            document.querySelector('.content-wrapper')?.scrollTo({ top: 0, behavior: 'auto' });
            document.documentElement.scrollTop = 0;
        } catch (error) {
            window.location.href = url;
        } finally {
            hideLoader();
        }
    }

    document.addEventListener('click', function(e) {
        const link = e.target.closest('.sidebar a.nav-link');

        if (!link || !link.href || link.getAttribute('href') === '#') {
            return;
        }

        const isSameOrigin = link.origin === window.location.origin;
        const isModifiedClick = e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0;
        const isUnsupported = link.target === '_blank' || link.hasAttribute('download') || link.href.startsWith('javascript:');

        if (!isSameOrigin || isModifiedClick || isUnsupported) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        loadSidebarPage(link.href, link);
    }, true);

    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.sidebarUrl) {
            const link = Array.from(document.querySelectorAll('.sidebar a.nav-link[href]')).find(item => item.href === event.state.sidebarUrl);
            loadSidebarPage(event.state.sidebarUrl, link, false);
        }
    });

    /* ── PDF download helper (used by pages that push html2pdf) ── */
    function downloadPDF(elementId, filename) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const opt = {
            margin:     [8, 8, 8, 8],
            filename:   filename || 'document.pdf',
            image:      { type: 'jpeg', quality: 0.97 },
            html2canvas:{ scale: 2, useCORS: true, logging: false },
            jsPDF:      { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(el).save();
    }

    function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            showLoader();
            setTimeout(() => {
                document.getElementById('logout-form').submit();
            }, 100);
        }
    }

</script>

{{-- html2pdf.js — available globally for print/download on any page --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
