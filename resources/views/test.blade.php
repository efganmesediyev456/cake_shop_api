
@dd(Cart::instance('favourite')->content())
@foreach(Cart::instance('favourite')->content() as $c)
    {{$c->name}}
    @endforeach