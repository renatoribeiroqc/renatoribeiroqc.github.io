<?php 
use Lib\Framework\Helpers\Html;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <title>Conexperience</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Conexperience</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= Html::text(array("label" => 'MENU_ITEM_ADMIN_98')) ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URI . 'access/readroles' ?>"><?= Html::text(array("label" => 'MENU_ITEM_ADMIN_100')) ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URI . 'access/readpermissions' ?>"><?= Html::text(array("label" => 'MENU_ITEM_ADMIN_101')) ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URI . 'access/read' ?>"><?= Html::text(array("label" => 'MENU_ITEM_ADMIN_102')) ?></a></li>                                                        
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= Html::text(array("label" => 'MENU_ITEM_ADMIN_99')) ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="<?php echo BASE_URI . 'user/read' ?>"><?= Html::text(array("label" => 'MENU_ITEM_ADMIN_103')) ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URI . 'user/signup' ?>"><?= Html::text(array("label" => 'MENU_ITEM_ADMIN_104')) ?></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="?lang=en_CA">en_US</a>
                    </li>                    
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="?lang=pt_BR">pt_BR</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php require_once($this->partial) ?>

    <!-- Bootstrap JS (with Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>