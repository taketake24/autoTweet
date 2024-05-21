<?php

// プログラミング言語のリスト
$languages = [
    "PHP",
    "Python",
    "Ruby",
    "Go",
    "JavaScript",
    "Jquery",
    "Vue.js",
    "React",
    "html",
    "css",
    "Java",
    "node.js",
    "R",
    // "Google Apps Script",//文字数が多いので
    "Mysql",
    "TypeScript",
    "C++",
    "C#",
];

// テンプレートリスト
$templates = [
    "- About the annual salary for the programming language [***]",
    "- About the future prospects of the programming language [***]",
    "- Is the programming language [***] popular? The reasons why",
    "- Methods to improve skills in the programming language [***]",
    "- About income increase with the programming language [***]",
    "- Is the programming language [***] an essential skill, or not? The reasons why",
    "- The latest trends in the programming language [***]"
];

// 各言語ごとにテンプレートを適用する
foreach ($languages as $language) {
    // echo "### " . $language . "\n";
    foreach ($templates as $template) {
        $promptWord = str_replace("***", $language,$template);
        echo $promptWord . "\n";
    }
    // echo "\n";
}

?>