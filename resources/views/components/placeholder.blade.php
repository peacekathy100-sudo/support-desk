<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius:28px; overflow:hidden;">
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7 text-center text-lg-start">
                            <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(0, 102, 204, 0.12); color:#0066cc; font-weight:600; letter-spacing:0.02em;">
                                Flaxem Support Desk
                            </span>
                            <h1 class="mb-3" style="font-size:2.2rem; line-height:1.15; color:#102a43;">Official access to support, operations, and audit-ready reporting.</h1>
                            <p class="mb-4" style="font-size:1.02rem; color:#4a5568;">
                                Centralise tickets, client records, leave requests, and reporting in one secure workspace designed for everyday support teams and management oversight.
                            </p>
                            <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2" style="border-radius:999px;">Open Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2" style="border-radius:999px;">Login to Portal</a>
                                    <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius:999px;">Access Support Desk</a>
                                @endauth
                            </div>
                        </div>

                        <div class="col-lg-5 text-center">
                            <img src="{{ URL::asset('assets/images/centenary.png') }}" alt="Centenary Logo" style="max-height:120px; width:auto;">
                            <div class="mt-4 p-3 rounded-3" style="background:#f8fbff; border:1px solid rgba(0, 102, 204, 0.12);">
                                <p class="mb-1" style="font-size:0.9rem; color:#4a5568;">
                                    Fast, trackable, and prepared for real support desk operations.
                                </p>
                                <div class="d-flex justify-content-center gap-3 mt-3 text-start">
                                    <div>
                                        <strong style="display:block; color:#102a43;">Ticket flow</strong>
                                        <small style="color:#4a5568;">Create, assign, and monitor support activity.</small>
                                    </div>
                                    <div>
                                        <strong style="display:block; color:#102a43;">Audit-ready</strong>
                                        <small style="color:#4a5568;">Clear history and printable reports.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 h-100" style="background:#f8fbff; border:1px solid rgba(0, 102, 204, 0.12);">
                                <h5 class="mb-2" style="color:#102a43;">Tickets</h5>
                                <p class="mb-0" style="color:#4a5568; font-size:0.95rem;">Manage customer issues, updates, and assignments from one dashboard.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 h-100" style="background:#f8fbff; border:1px solid rgba(0, 102, 204, 0.12);">
                                <h5 class="mb-2" style="color:#102a43;">Clients</h5>
                                <p class="mb-0" style="color:#4a5568; font-size:0.95rem;">Maintain client records and keep all related support service activity connected.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 h-100" style="background:#f8fbff; border:1px solid rgba(0, 102, 204, 0.12);">
                                <h5 class="mb-2" style="color:#102a43;">Reports</h5>
                                <p class="mb-0" style="color:#4a5568; font-size:0.95rem;">Generate printable, receipt-style reports with audit trail detail for operations and review.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
