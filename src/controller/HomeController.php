$data = file_get_contents('../public/data/resultados.json');
$resultados = json_decode($data, true);

echo $twig->render('index.twig', [
    'resultados' => $resultados
]);
