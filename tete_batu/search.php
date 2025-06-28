$q = "%".$_GET['q']."%";
$stmt = $pdo->prepare("SELECT * FROM spots WHERE nama LIKE ?");
$stmt->execute([$q]);
$results = $stmt->fetchAll();
