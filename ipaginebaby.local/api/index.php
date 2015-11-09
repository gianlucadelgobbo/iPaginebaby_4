<?
include_once("../_php/site.class.php");
$lang = $_GET['lang'] ? $_GET['lang'] : $_POST['lang'];
$site = new site($lang);
switch ($_GET['param']) {
	case "explore":
		if ($_POST['id']) {
			print_r($site->dataObj->searchDett($_POST['id']));
		} else if ($_GET['fav']) {
			print_r($site->dataObj->searchFav($_GET['fav']));
		} else {
			print_r($site->dataObj->searchPlaces($_GET['q'],$_GET['ll'],$_GET['radius'],$_GET['cat'],$_GET['skip']));
		}
		break;
	case "newplace":
		if (isset($_POST['pbcard'])) {
			print_r($site->dataObj->savePlace($_POST));
		} else {
			print_r($site->dataObj->newPlace($_POST));
		}
		break;
	case "rate":
		print_r($site->dataObj->ratePlace($_GET));
		break;
	case "favorities":
		print_r($site->dataObj->favPlace($_GET));
		break;
	case "checkin":
		print_r($site->dataObj->checkinPlace($_GET));
		break;
	case "message":
		print_r($site->dataObj->sendMessage($_POST));
		break;
	case "delete":
		print_r($site->dataObj->deletePlace($_POST));
		break;
	case "login":
//		print_r(json_encode($_POST));
		print_r($site->dataObj->logLogin($_POST));
		break;
	default:
		print_r(json_encode(array("msg"=>"che vuoi?")));
		break;
}
?>