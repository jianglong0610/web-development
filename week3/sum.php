<html>

<head>
    <title>exercise3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</head>

<body>
    <div class="d-flex flex-wrap">
    <?php
    $plus= "+" ;
    $sum=0 ;

    for ($num = 1; $num <= 100; $num++) {
        if($num == 100)
        {
            $plus = "";
        }
        if($num % 2 ==0) 
    {
        echo "<strong>" . "<p class=\"d-flex\">$num $plus</p>" . "</strong>";
    }
        else
        {
            echo "<p class=\"d-flex\">$num $plus</p>"; 
        }
        
        $sum=$sum+$num;
        
    }
        echo "<strong><p class=\"d-flex\"> = $sum</p></strong>";
    
    ?>
</div>
</body>


</html>