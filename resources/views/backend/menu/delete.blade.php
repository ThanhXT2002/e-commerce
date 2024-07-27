@extends('backend.layout.layoutadmin')
@section('content')
    @include('backend.layout.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
    <section class="content mt-2">
        @include('backend.layout.component.formError')
        <form action="{{ route('menu.destroy', $menuCatalogue->id) }}" method="post" class="box">
            @include('backend.layout.component.destroy', ['model' => $menuCatalogue])
         </form>
    </section>
@endsection