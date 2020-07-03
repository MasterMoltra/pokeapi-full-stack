<!DOCTYPE html>
<html lang="en">
<?php
include_once __DIR__ . '/partials/head.php';
?>

<body>
    <?php
    include_once __DIR__ . '/partials/header.php';
    ?>
    <div class="container" role="main">
        <h3>Discover useful infos about your favorite Pok&eacute;mon:</h3>
        <form action="" method="post">
            <fieldset>
                <div class="form-input-group">
                    <label>Pok&eacute;mon name:</label><input type="text" name="pokeapi-name" placeholder="Enter a valid name, e.g.bulbasaur">
                </div>
                <div class="form-input-group">
                    <span>Send the request to:</span>
                    <input type="radio" name="pokeapi-mode" value="php" checked> <label>PHP</label>
                    &nbsp;&nbsp;
                    <input type="radio" name="pokeapi-mode" value="node"> <label>NODE</label>
                </div>
                <button id="pokeapi-send" type="submit" title="Send">Send</button>
            </fieldset>
        </form>
        <div id="content" class="content"> </div>
        <h4 id="pokeapi-path"><span>www.masterpoke.co/pokemon</span></h4>
    </div>
    <?php
    include_once __DIR__ . '/partials/footer.php';
    ?>
</body>

</html>