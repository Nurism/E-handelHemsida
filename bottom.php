    <footer>
        <p>&copy; 2026 VenueNow</p>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var statusMessage = document.querySelector('.popup-message');
            if (!statusMessage) {
                return;
            }

            setTimeout(function() {
                statusMessage.style.opacity = '0';
                statusMessage.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    if (statusMessage.parentNode) {
                        statusMessage.parentNode.removeChild(statusMessage);
                    }
                }, 350);
            }, 3200);
        });
    </script>
</body>
</html>