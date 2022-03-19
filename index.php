<?php

function getReposData($repos, $apiHelper): array{
    $reposData = [];
    $i = 0;
    foreach ($repos as $repo) {
        $contributors = $apiHelper->makeRequest($repo->contributors_url);
        $numberOfContributors = $contributors != NULL ? count($apiHelper->makeRequest($repo->contributors_url)) : 0;
        $fork = $repo->fork ? $apiHelper->makeRequest($repo->url)->parent->html_url : "Brak";
        $reposData[] = [
            'name' => $repo->name,
            'url' => $repo->html_url,
            'fork' => $fork,
            'contributors' => $numberOfContributors
        ];
        $i++;
        if($i > 6) break;
    }
    return $reposData;
}

function sortRepos($sort, $reposData) : array{
    foreach ($reposData as $key => $val) {
        $namesArr[$key] = $val['name'];
        $contributorsArr[$key] = $val['contributors'];
    }
    $direction = in_array($sort, [1,2]) ? SORT_ASC : SORT_DESC;
    $value = in_array($sort, [1,3]) ? $namesArr : $contributorsArr;
    array_multisort($value, $direction, $reposData);
    return $reposData;
}

if(!empty($_GET)) {
    require_once 'class/ApiHandler.php';
    $organisation = $_GET['organisation'];
    $apiHelper = new ApiHandler();
    $reposUrl = 'https://api.github.com/orgs/' . $organisation . '/repos';
    $repos = $apiHelper->makeRequest($reposUrl);

    $reposData = getReposData($repos, $apiHelper);
    $sort = $_GET['sort'] ?? 1;
    if($sort) {
        $reposData = sortRepos($sort, $reposData);
    }

    $reposTable = "<br><table><tr>
        <th><a href='/?organisation=". $organisation ."&sort=". ($sort == 1 ? 3 : 1) . "'>Nazwa";

    if($sort == 1) $reposTable .= " &darr;";
    elseif ($sort == 3) $reposTable .= " &uarr;";

    $reposTable.="</a></th><th>Fork</th>
        <th><a href='/?organisation=". $organisation ."&sort=". ($sort == 2 ? 4 : 2) . "'>Kontrybutorzy";

    if($sort == 2) $reposTable .= " &darr;";
    elseif ($sort == 4) $reposTable .= " &uarr;";

    $reposTable .= "</a></th></tr>";

    foreach ($reposData as $repo){
        $reposTable .= "<tr>
                <td><a href='" . $repo['url'] . "'>" . $repo['name'] . "</a></td>
                <td>" . $repo['fork'] . "</td>
                <td>" . $repo['contributors'] . "</td>
            </tr>";
    }

    $reposTable .= "</table>";
}

$view = file_get_contents('view/main.html');
$reposTable = $reposTable ?? '';
$view = str_replace('[%repos_table%]', $reposTable, $view);
$organisation = $organisation ?? '';
$view = str_replace('[%organisation%]', $organisation, $view);

echo $view;