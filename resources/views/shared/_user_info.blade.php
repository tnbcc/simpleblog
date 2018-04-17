<a href="{{ route('users.show',$user->id) }}">
  <img src="{{ URL::asset('storage'.$user->img_path) }}" alt=" {{ $user->name }} " class="gravatar"/>
</a>
<h1>{{ $user->name }}</h1>
