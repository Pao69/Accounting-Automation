        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

    // Highlight active menu item
    const currentPath = window.location.pathname;
    $('nav a').each(function() {
        if ($(this).attr('href') === currentPath) {
            $(this).closest('li').addClass('active');
            $(this).closest('.collapse').addClass('show');
        }
    });
});
</script>
</body>
</html> 