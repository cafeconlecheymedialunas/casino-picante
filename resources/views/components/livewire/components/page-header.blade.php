@props([
    'title' => '',
    'subtitle' => '',
    'buttonText' => '',
    'buttonAction' => '',
])

<livewire:components.page-header
    :title="$title"
    :subtitle="$subtitle"
    :button-text="$buttonText"
    :button-action="$buttonAction"
    :key="'page-header-'.md5($title.$subtitle.$buttonText.$buttonAction)"
/>
