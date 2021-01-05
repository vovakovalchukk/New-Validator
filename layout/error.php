<div class="mar30-tb b-center w500px pad30-rl pad10-tb bordered rounded">

    <h1>Form error</h1>
    
    <?

    $allErrors = $validator -> getValidatorValue("errors");
    $errorsValues = [];
    foreach ($allErrors as $key => $value){
        foreach ($value as $val){
            $errorsValues[] = "($key) -" . " " . $val;
        }
    }

    ?>

    <ul><li><?= implode('<li>', $errorsValues) ?></ul>
    
</div>  