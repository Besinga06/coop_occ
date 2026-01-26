<!DOCTYPE html>
<html>
<head> 
<title>List of code</title>
</head>
<body>
    
<div id="alldiv">
<div id="foo" style=" background: red; max-height: 100px; " >
   <?php 

     $code = $barcode;
     for ($i=0; $i < $quantity; $i++) { 
?>
   <div class="bar" style=" width: 120px; height: 120px; float: left; margin: 1em; ">
      <img alt="<?= $code ?>" src="barcode.php?codetype=Codabar&size=40&text=<?=$code?>&print=true" /> 
   </div> 
   <?php } ?>

</div>
</div>


</body>
</html>