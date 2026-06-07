<?php
$flash = getFlash();
if (!$flash) return;

$typeMap = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
];

$cssClass = $typeMap[$flash['type']] ?? 'alert-info';
?>
<div id="flash-alert" class="alert <?= $cssClass ?> alert-dismissible fade show mx-3 mt-2" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>

<script>
    setTimeout(function () {
        var alert = document.getElementById('flash-alert');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(function () { alert.remove(); }, 300);
        }
    }, 3000);
</script>
