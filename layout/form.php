<div class="mar30-tb b-center w500px pad30-rl pad10-tb bordered rounded">
    
    <h1>Form demo</h1>

    <form method="POST">

        <div class="mar10-b">type Email (Required!)</div>
        <input class="w100" type="email" name="email" value=""> 

        <div class="mar10-b mar20-t">type Number от 5 до 9 (Required!)</div>
        <input class="w100" type="number" name="number" value="">

        <div class="mar10-b mar20-t">type Url</div>
        <input class="w100" type="url" name="url" value="">

        <div class="mar10-b mar20-t">type Checkbox multiple (Required!) (2-3 checked values)</div>
        <input type="checkbox" name="checkboxies[]" value="Значение 1"><span> Значение 1</span>
        <input type="checkbox" name="checkboxies[]" value="Значение 2"><span> Значение 2</span>
        <input type="checkbox" name="checkboxies[]" value="Значение 3"><span> Значение 3</span>
        <input type="checkbox" name="checkboxies[]" value="Значение 4"><span> Значение 4</span>
        
        <div class="mar10-b mar20-t">type Text (Required!) custom multiple (2-3 values) with custom pattern regexp (^.{3,20}$)</div>
        <input class="w100" type="text" name="text" value="">

        <div class="mar10-b mar20-t">type Text(custom & custom multiple explode ",") filter emails</div>
        <input class="w100" type="text" name="text2" value="">
        
        <div><button class="mar20-tb" type="submit">Submit</button></div>

    </form>

</div>  