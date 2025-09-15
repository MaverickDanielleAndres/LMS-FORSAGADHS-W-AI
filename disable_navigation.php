<?php
echo <<<EOD
<script type="text/javascript">
// Prevent back button after login
(function () {
    // Always add a new history entry
    history.pushState(null, document.title, location.href);
    window.addEventListener('popstate', function () {
        history.pushState(null, document.title, location.href);
    });

    // Also block backspace and alt key navigation
    document.addEventListener("keydown", function (e) {
        const tag = e.target.tagName.toLowerCase();
        const isEditable = tag === "input" || tag === "textarea" || e.target.isContentEditable;
        if ((e.key === "Backspace" && !isEditable) || (e.altKey && (e.key === "ArrowLeft" || e.key === "ArrowRight"))) {
            e.preventDefault();
        }
    });
})();
</script>
EOD;
?>
