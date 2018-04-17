<a href="{{ route('users.show',$user->id) }}">
  <img src="{{ URL::asset($user->img_path) }}" alt=" {{ $user->name }} " class="gravatar"/>
</a>
<h1>{{ $user->name }}</h1>
