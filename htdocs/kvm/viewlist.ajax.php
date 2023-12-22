<?php
$dir = '../print/kvm';
$files = scandir($dir);
echo '<div class="container" style="display: flex; justify-content: space-between; flex-wrap: wrap;">'; // 新增的包裹層

$count = 0; // 計數器

for ($i = 2; $i < sizeof($files); $i++) {
    if (substr($files[$i], -4) == '.ini') {
        $f = parse_ini_file('../print/kvm/' . $files[$i], true);

        echo '<div style="width: 48%;">'; // 調整這裡的 width
        echo '<div>' . $f['list']['title'] . '</div>';
        echo '<div>會員名稱:' . $f['Name']['Namee'] . '</div>';
        echo '<div>車牌號碼:' . $f['CarNumber']['CarNamee'] . '</div>';
        echo '<div style="border-bottom:1px #ffffff solid;">';
        for ($index = 0; $index < sizeof($f['item']['label']); $index++) {
            echo '<span>' . $f['item']['label'][$index] . '</span> ';
        }
        echo '</div>';
        echo '</div>';

        if ($count % 2 != 0) {
			$count++;
            echo '<div style="width: 100%;"></div>'; // 換行
        }
    } else {
    }
}

echo '</div>'; // 包裹層的結尾
?>
