<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(3000);
$json = '{"item":[{"pubDate":"Thu, 30 Mar 2017 19:27:40 -0600","title":"Contacto","url":"contacto","meta":{},"metad":{},"menu":"Contacto","menuOrder":"20","menuStatus":"Y","template":"template.php","parent":"No Parent","private":{},"author":"somos","slug":"contacto","filename":"contacto.xml"},{"pubDate":"Fri, 16 Dec 2016 19:41:40 -0600","title":"Welcome to GetSimple!","url":"index","meta":"getsimple, easy, content management system","metad":{},"menu":"Home","menuOrder":"1","menuStatus":"Y","template":"template.php","parent":"No Parent","private":{},"author":"somos","slug":"index","filename":"index.xml"},{"pubDate":"Fri, 16 Dec 2016 21:04:12 -0600","title":"Notas Generales - No borrar","url":"notas","meta":{},"metad":{},"menu":"Notas Generales","menuOrder":"0","menuStatus":{},"template":"template.php","parent":"No Parent","private":{},"author":"somos","slug":"notas","filename":"notas.xml"},{"pubDate":"Thu, 30 Mar 2017 12:55:55 -0600","title":"Precio","url":"precio-1","meta":{},"metad":{},"menu":"Precio","menuOrder":"0","menuStatus":"Y","template":"template.php","parent":"sub-producto-2","private":{},"author":"somos","slug":"precio-1","filename":"precio-1.xml"},{"pubDate":"Thu, 30 Mar 2017 12:55:01 -0600","title":"Precio","url":"precio","meta":{},"metad":{},"menu":"Precio","menuOrder":"0","menuStatus":"Y","template":"template.php","parent":"sub-producto-1","private":{},"author":"somos","slug":"precio","filename":"precio.xml"},{"pubDate":"Thu, 30 Mar 2017 12:52:55 -0600","title":"Productos","url":"productos","meta":{},"metad":{},"menu":"Productos","menuOrder":"2","menuStatus":"Y","template":"template.php","parent":"No Parent","private":{},"author":"somos","slug":"productos","filename":"productos.xml"},{"pubDate":"Fri, 31 Mar 2017 12:08:11 -0600","title":"Sub Producto 5","url":"sub-producto-1","meta":{},"metad":{},"menu":"Sub 5","menuOrder":"1","menuStatus":"Y","template":"template.php","parent":"productos","private":{},"author":"somos","slug":"sub-producto-1","filename":"sub-producto-1.xml"},{"pubDate":"Fri, 31 Mar 2017 12:08:27 -0600","title":"Sub Producto 2","url":"sub-producto-2","meta":{},"metad":{},"menu":"Sub 2","menuOrder":"2","menuStatus":"Y","template":"template.php","parent":"productos","private":{},"author":"somos","slug":"sub-producto-2","filename":"sub-producto-2.xml"}]}';

$array = json_decode($json)->item;
$new=array();
$index=0;
foreach ($array as $key => $value) {
	if ($value->menuStatus != 'Y') {
		unset($array[$key]);
		sort($array);
	}
	$value->childrens=array();
}
while (count($array)) {

	/*dandole posibilidad a que todos tengan hijos*/
	if (!isset($array[$index])) {
		$index=0;
		sort($array);
	}
	if ($array[$index]->menuStatus == 'Y' ) {
		// echo $index."<br>";
		// echo $array[$index]->slug."<br>";
		if (strtolower($array[$index]->parent) == 'no parent') {
			// echo $array[$index]->slug."<br>";

			$new[]=$array[$index];
			unset($array[$index]);
			// echo count($array)."<br>";
			sort($array);		
		}else{
		
			buscar($array[$index],$new,$array,$index);
		}
	}
	if ($index > count($array)) {
		$index=0;
	}
	
	$index++;
}


function buscar($hijo,&$childs,&$quitar,&$index){

	if (count($childs)) {
		/*recorriendo arreglo de hijos*/
		foreach ($childs as $key => $value) {
			/*validando que sea aprobado*/
			if ($value->menuStatus == 'Y') {
				/*comparando que el hijo tenga como padre a algun hijo del arreglo de hijos*/
				// echo "Pasando por $value->slug padre $hijo->parent <br>";
				if ($hijo->parent == $value->slug) {
					/*asignando hijo a grupo de hijos*/
					// echo "juntando $value->slug -> $hijo->slug <br>";
					$value->childrens[] = $hijo;
					// echo $hijo->slug."<br>";
					unset($quitar[$index]);
					sort($quitar);
					
					
				}else{

					/*si tiene mas hijos*/
					if (count($value->childrens)) {
						/*buscar si es hijo de algun hijo*/
						buscar($hijo,$value->childrens,$quitar,$index);
					}
				}
			}else{
				unset($childs[$key]);
				sort($childs);
			}
		}
	}
}
// echo "<pre>";
// print_r($new);

$ordenado = array();
$index=0;
usort($new, function($a, $b) {
    return $a->menuOrder - $b->menuOrder;
});

function print_menu($menu){
	echo "<ul>";
	foreach ($menu as $key => $value) {
		if ($value->parent == 'No Parent') {
			echo "<li> <a href='".$value->url."' title='".$value->title."'>$value->menu </a> ";
			if (count($value->childrens)) {
				print_menu($value->childrens);
			}
			echo "</li>";
		}else{
			echo "<li> <a href='".$value->url."' title='".$value->title."'>$value->menu </a> ";
			if (count($value->childrens)) {
				print_menu($value->childrens);
			}
			echo "</li>";
		}
	}
	echo "</ul>";

}





print_menu($new);
// var_dump(count($array));