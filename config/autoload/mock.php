<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    // 数据模式配置
    // true: 使用Mock数据 (开发环境)
    // false: 使用真实数据 (生产环境)
    'is_mock' => (bool)env('IS_MOCK', true),
];
