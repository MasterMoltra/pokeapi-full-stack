<!DOCTYPE html>
<html lang="en">
<?php
include_once __DIR__ . '/partials/head.php';
?>

<body>
    <?php
    include_once __DIR__ . '/partials/header.php';
    $radio_local = $tpl__local ? ' checked' : ' disabled';
    $rdadio_api = $tpl__local ? '' : ' checked';
    ?>
    <div class="container" role="main">
        <h3><?php echo  $tpl__msg ?? '';  ?> Discover useful infos about your favorite Pok&eacute;mon:</h3>
        <form action="" method="post">
            <fieldset>
                <div class="form-input-group">
                    <label>Pok&eacute;mon name:</label><input type="text" name="pokeapi-name" placeholder="Enter a valid name, e.g. bulbasaur">
                </div>
                <div class="form-input-group radio">
                    <span>Send the request to</span>
                    <input type="radio" name="pokeapi-mode" value="local" <?php echo  $radio_local ?>> <label>LOCAL</label>
                    &nbsp;&nbsp;
                    <input type="radio" name="pokeapi-mode" value="api" <?php echo  $rdadio_api ?>> <label>API</label>
                </div>
                <div class="form-input-group radio">
                    <span>With language </span>
                    <input type="radio" name="pokeapi-plang" value="php" checked> <label>PHP</label>
                    &nbsp;&nbsp;
                    <input type="radio" name="pokeapi-plang" value="node"> <label>NODE</label>
                </div>
                <button id="pokeapi-send" type="submit" title="Send">Send</button>
            </fieldset>
        </form>
        <div id="content" class="content"> </div>
        <h4 id="pokeapi-path"></h4>
    </div>
    <?php
    include_once __DIR__ . '/partials/footer.php';
    ?>
</body>

</html>