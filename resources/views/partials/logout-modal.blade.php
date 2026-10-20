<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="logout-icon"><img src="{{ asset('images/odin-mark.svg') }}" alt="Odin.lk" width="56" height="56"></div>
                <h5 id="logoutModalLabel" class="fw-bold mt-3 mb-2">Log out?</h5>
                <p class="text-muted small mb-4">
                    Are you sure you want to log out{{ ($area ?? 'store') === 'admin' ? ' of the admin panel' : ' of your Odin.lk account' }}?
                    @if (($cartCount ?? 0) > 0)
                        <br><span class="text-danger">The {{ $cartCount }} {{ \Illuminate\Support\Str::plural('item', $cartCount) }} in your cart will be removed.</span>
                    @endif
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light border flex-fill" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-odin flex-fill" id="confirmLogout">Yes, log out</button>
                </div>
            </div>
        </div>
    </div>
</div>
