<div class="mar30-tb b-center w500px pad30-rl pad10-tb bordered rounded">

    <h1>Form result</h1>

    <?

    $allSuccsessResults = $validator -> getValidatorValue("resultData");
    $resultsValues = [];
    foreach ($allSuccsessResults as $key => $value){
        foreach ($value as $val){
            $resultsValues[] = "($key) -" . " " . $val;
        }
    }

    ?>

    <ul><li><?= implode('<li>', $resultsValues) ?></ul>
    
</div>  