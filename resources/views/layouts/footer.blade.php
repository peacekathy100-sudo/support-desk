<style>
    .fixed-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
        z-index: 1000;
        background: linear-gradient(90deg, #3496D7 0%, #3496D7 100%);
        box-shadow: 0 -2px 5px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        color: #fff;
    }

    .fixed-footer .footer-brand,
    .fixed-footer .footer-timer {
        color: #fff;
    }

    body {
        padding-bottom: 70px;
    }

    .sidebar {
        max-height: 100vh;
        overflow-y: auto;
        padding-bottom: 20px;
    }

    .content {
        padding-bottom: 30px;
    }
</style>


<div class="navbar navbar-sm navbar-footer border-top fixed-footer">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="footer-brand"><strong>SUPPORT DESK TICKETING</strong></span>
        <span class="footer-timer">
            Session Timer:
            <strong id="footer-timer">10:00</strong>
        </span>
    </div>
</div>
<script>
let time = 600;
function updateFooterTimer() {
    let minutes = Math.floor(time / 60);
    let seconds = time % 60;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    document.getElementById("footer-timer").innerHTML = minutes + ":" + seconds;
    if (time > 0) {
        time--;
    } else {
        clearInterval(timerInterval);
        alert("Session expired!");
        window.location.href = "{{ route('session.expired') }}";
    }
}
let timerInterval = setInterval(updateFooterTimer, 1000);
updateFooterTimer();
</script>

