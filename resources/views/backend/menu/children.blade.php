@extends('backend.layout.layoutadmin')

@section('content')
    @include('backend.layout.component.breadcrumb', [
        'title' =>
            $config['seo'][$config['method']]['title'] .
            ' ' .
            ucfirst(strtolower($menu->languages->first()->pivot->name)),
    ])
    <section class="content mt-4">
        @include('backend.layout.component.formError')
        @php
            $url = $config['method'] == 'create' ? route('menu.store') : (($config['method'] == 'children') ? route('menu.save.children', $menu->id) : route('menu.update', $menu->id));
        @endphp
        <form action="{{ $url }}" method="post" class="box">
            @csrf
            <div class="wrapper wrapper-content animated fadeInRight my-2">

                @include('backend.menu.component.list')
            </div>
            @include('backend.layout.component.btnsubmit')
        </form>
        @include('backend.menu.component.popup')
    </section>
@endsection
