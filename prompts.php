<?php
// prompts.php

function get_prompts_data_path() {
    return __DIR__ . '/data/prompts.json';
}

function load_prompts() {
    $path = get_prompts_data_path();
    
    // Default built-in prompts
    $default_built_in = [
        [
            'name' => '文本润色',
            'prompt' => '请将以下文本进行润色，使其表达更清晰、流畅、专业，同时保持原有核心意思。请注意修正语法错误、调整句子结构、并使用更丰富的词汇。需要润色的文本如下：{user_input}'
        ],
        [
            'name' => '文本排版',
            'prompt' => '请将以下内容进行智能排版，使其结构清晰、易于阅读。你可以使用标题、列表（项目符号或数字编号）、段落、重点标记（如粗体）等元素来优化格式。原文如下：{user_input}'
        ],
        [
            'name' => '关键词提炼',
            'prompt' => '请从以下文本中提炼出核心关键词，通常是 3 到 5 个，并按重要性降序排列。关键词应能高度概括文本的主题。原文如下：{user_input}'
        ],
        [
            'name' => '满分作文',
            'prompt' => '请根据以下作文题目或要求，创作一篇高质量的满分作文。要求结构完整、论点清晰、论据充分、语言优美生动。作文题目或要求是：{user_input}'
        ],
        [
            'name' => '情书写作',
            'prompt' => '请帮我写一封深情款款的情书。你可以参考以下背景信息和我想表达的情感，请用真挚、浪漫且不落俗套的语言来书写。背景信息：{user_input}'
        ],
        [
            'name' => '古诗创作',
            'prompt' => '请根据以下主题或意境，创作一首中国古典风格的诗词（如五言绝句、七言律诗或宋词）。要求意境优美，格律基本正确，富有文采。创作主题：{user_input}'
        ],
        [
            'name' => '个人简历优化',
            'prompt' => '请根据我提供的个人简历信息，优化其中的某一段经历描述或整体排版建议，使其更具吸引力，突出我的核心优势和成就。请注意使用STAR法则（Situation, Task, Action, Result）来优化经历描述。我的简历信息如下：{user_input}'
        ],
        [
            'name' => '小说大纲',
            'prompt' => '请根据我提供的小说核心创意，生成一个完整的小说大纲。大纲需要包括：故事背景设定、主要人物简介（包含性格和动机）、核心冲突、以及分章节（或分幕）的剧情梗概。我的核心创意是：{user_input}'
        ],
        [
            'name' => '人物/品牌起名',
            'prompt' => '我需要为我的小说角色、游戏角色、新品牌或新产品起一个名字。请根据我提供的以下背景信息，提供 5 个有创意、有寓意且易于记忆的名字，并简要说明每个名字的含义或灵感来源。背景信息：{user_input}'
        ],
        [
            'name' => '翻译',
            'prompt' => '请将以下文本翻译成目标语言。如果未指定目标语言，请默认翻译成英文。原文：{user_input}'
        ]
    ];
    
    // Sort built-in prompts by name for consistent order
    usort($default_built_in, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    if (!file_exists($path)) {
        $initial_data = [
            'built_in' => $default_built_in,
            'user_defined' => []
        ];
        if (file_put_contents($path, json_encode($initial_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            throw new Exception("Could not create prompts.json file.");
        }
        return $initial_data;
    }

    $json_content = file_get_contents($path);
    if ($json_content === false) {
        throw new Exception("Could not read prompts.json file.");
    }

    $data = json_decode($json_content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding prompts.json: " . json_last_error_msg());
    }

    // --- Sync built-in prompts ---
    // This ensures that if we update the built-in prompts in code,
    // the changes are reflected for the user without deleting their custom prompts.
    $data['built_in'] = $default_built_in;
    // We can optionally re-save the file to persist the updated built-in prompts
    // file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    return $data;
}


function add_user_prompt($name, $text) {
    $path = get_prompts_data_path();
    $data = load_prompts();

    // Check for duplicates in user-defined prompts
    foreach ($data['user_defined'] as $prompt) {
        if (strtolower($prompt['name']) === strtolower($name)) {
            throw new Exception("A user-defined prompt with this name already exists.");
        }
    }
    // Check for duplicates in built-in prompts
    foreach ($data['built_in'] as $prompt) {
        if (strtolower($prompt['name']) === strtolower($name)) {
            throw new Exception("A built-in prompt with this name already exists.");
        }
    }

    $new_prompt = ['name' => $name, 'prompt' => $text];
    $data['user_defined'][] = $new_prompt;
    
    // Sort user-defined prompts by name for consistent order
    usort($data['user_defined'], function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}


function delete_user_prompt($name) {
    $path = get_prompts_data_path();
    $data = load_prompts();
    
    $initial_count = count($data['user_defined']);
    $data['user_defined'] = array_filter($data['user_defined'], function($prompt) use ($name) {
        return $prompt['name'] !== $name;
    });
    
    // Re-index the array to avoid it becoming an object in JSON
    $data['user_defined'] = array_values($data['user_defined']);

    if (count($data['user_defined']) === $initial_count) {
         throw new Exception("Prompt to delete was not found.");
    }
    
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

?>
