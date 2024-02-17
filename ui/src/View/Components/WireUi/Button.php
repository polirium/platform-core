<?php

namespace Polirium\Core\UI\View\Components\WireUi;

class Button extends BaseButton
{
    public function outlineColors(): array
    {
        return [
            self::DEFAULT   => "",
            'primary'       => "btn-outline-primary",
            'secondary'     => "btn-outline-secondary",
            'success'       => "btn-outline-success",
            'danger'        => "btn-outline-danger",
            'positive'      => "btn-outline-success",
            'negative'      => "btn-outline-danger",
            'warning'       => "btn-outline-warning",
            'info'          => "btn-outline-info",
            'white'         => "btn-outline-light",
            'light'         => "btn-outline-light",
            'dark'          => "btn-outline-dark",
            'blue'          => "btn-outline-blue",
            'azure'         => "btn-outline-azure",
            'indiago'       => "btn-outline-indiago",
            'purple'        => "btn-outline-purple",
            'pink'          => "btn-outline-pink",
            'red'           => "btn-outline-red",
            'orange'        => "btn-outline-orange",
            'yellow'        => "btn-outline-yellow",
            'lime'          => "btn-outline-lime",
            'green'         => "btn-outline-green",
            'teal'          => "btn-outline-teal",
            'cyan'          => "btn-outline-cyan",
        ];
    }

    public function flatColors(): array
    {
        return [
            self::DEFAULT   => "",
            'primary'       => "btn-ghost-primary",
            'secondary'     => "btn-ghost-secondary",
            'success'       => "btn-ghost-success",
            'danger'        => "btn-ghost-danger",
            'positive'      => "btn-ghost-success",
            'negative'      => "btn-ghost-danger",
            'warning'       => "btn-ghost-warning",
            'info'          => "btn-ghost-info",
            'white'         => "btn-ghost-light",
            'light'         => "btn-ghost-light",
            'dark'          => "btn-ghost-dark",
            'blue'          => "btn-ghost-blue",
            'azure'         => "btn-ghost-azure",
            'indiago'       => "btn-ghost-indiago",
            'purple'        => "btn-ghost-purple",
            'pink'          => "btn-ghost-pink",
            'red'           => "btn-ghost-red",
            'orange'        => "btn-ghost-orange",
            'yellow'        => "btn-ghost-yellow",
            'lime'          => "btn-ghost-lime",
            'green'         => "btn-ghost-green",
            'teal'          => "btn-ghost-teal",
            'teal'          => "btn-ghost-teal",
            'cyan'          => "btn-ghost-cyan",
        ];
    }

    public function defaultColors(): array
    {
        return [
            self::DEFAULT   => "",
            'primary'       => "btn-primary",
            'secondary'     => "btn-secondary",
            'success'       => "btn-success",
            'danger'        => "btn-danger",
            'positive'      => "btn-success",
            'negative'      => "btn-danger",
            'warning'       => "btn-warning",
            'info'          => "btn-info",
            'white'         => "btn-light",
            'light'         => "btn-light",
            'dark'          => "btn-dark",
            'blue'          => "btn-blue",
            'azure'         => "btn-azure",
            'indiago'       => "btn-indiago",
            'indiago'       => "btn-indiago",
            'purple'        => "btn-purple",
            'purple'        => "btn-purple",
            'pink'          => "btn-pink",
            'pink'          => "btn-pink",
            'red'           => "btn-red",
            'red'           => "btn-red",
            'orange'        => "btn-orange",
            'orange'        => "btn-orange",
            'yellow'        => "btn-yellow",
            'yellow'        => "btn-yellow",
            'lime'          => "btn-lime",
            'lime'          => "btn-lime",
            'green'         => "btn-green",
            'green'         => "btn-green",
            'teal'          => "btn-teal",
            'teal'          => "btn-teal",
            'cyan'          => "btn-cyan",
        ];
    }

    public function sizes(): array
    {
        return [
            'xs'          => 'btn-xs',
            'sm'          => 'btn-sm',
            self::DEFAULT => '',
            'lg'          => 'btn-lg',
        ];
    }

    public function iconSizes(): array
    {
        return [
            'xs'          => 'btn-xs',
            'sm'          => 'btn-sm',
            self::DEFAULT => '',
            'lg'          => 'btn-lg',
        ];
    }
}
