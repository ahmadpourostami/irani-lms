<?php

declare(strict_types=1);

namespace IraniLMS\Domain\Assessment;

use IraniLMS\Support\ServiceProviderInterface;

final class AssessmentServiceProvider implements ServiceProviderInterface {
    private QuizPostType $post_type;
    private QuizMeta $meta;
    private Quiz $quiz;

    public function register(): void {
        $this->post_type = new QuizPostType();
        $this->meta = new QuizMeta();
        $this->quiz = new Quiz();
    }

    public function boot(): void {
        add_action( 'init', [ $this->post_type, 'register' ] );
    }

    public function meta(): QuizMeta {
        return $this->meta;
    }

    public function quiz(): Quiz {
        return $this->quiz;
    }
}
