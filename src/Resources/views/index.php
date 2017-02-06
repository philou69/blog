<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>index</title>
</head>
<body>
    <div>welcome</div>
    <div>
        <?php
        foreach ($listChapitres as $chapitre){
            ?>
            <h5><?php echo $chapitre->getTitle() ?></h5>
        <?php
        }
        ?>
    </div>
</body>
</html>