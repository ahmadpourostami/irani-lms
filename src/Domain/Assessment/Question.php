<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

defined( 'ABSPATH' ) || exit;

final class Question {
    public const TYPE_SINGLE = 'single_choice';
    public const TYPE_MULTIPLE = 'multiple_choice';
    public const TYPE_TRUE_FALSE = 'true_false';
    public const TYPE_TEXT = 'short_text';

    public function __construct(
        private readonly string $id,
        private readonly string $type,
        private readonly string $text,
        private readonly array $options = [],
        private readonly mixed $answer = null,
        private readonly int $points = 1
    ) {}

    public function to_array(): array {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'text' => $this->text,
            'options' => $this->options,
            'answer' => $this->answer,
            'points' => max( 0, $this->points ),
        ];
    }
}
