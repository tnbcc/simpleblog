@extends('layouts.default')
@section('title', '更新个人资料')

@section('content')
<div class="col-md-offset-2 col-md-8">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h5>更新个人资料</h5>
    </div>
      <div class="panel-body">

        @include('shared._errors')
 <form method="POST" action="{{ route('users.update', $user->id )}}" enctype="multipart/form-data">
        <div class="gravatar_edit">
          <a href="{{ route('users.show',$user->id) }}">
            <img src="{{ URL::asset($user->img_path) }}" alt=" {{ $user->name }} " class="gravatar"/>
          </a>

        </div>
        <div class="form-group">
          <label for="img_path">头像：</label>

          <div id="addCommodityIndex">


                       <!--input-group start-->
                      <div class="input-group row">

                          <div class="col-sm-9 big-photo">
                          	<div id="preview">
                                  <img id="imghead" border="0" src="{{ URL::asset('/images/photo_icon.png') }}" width="90" height="90" onclick="$('#previewImg').click();" />
                               </div>
                              <input type="file" onchange="previewImage(this)" style="display: none;" id="previewImg" name="photo">
                          	<!--<input id="uploaderInput" class="uploader__input" style="display: none;" type="file" accept="" multiple="">-->
                          </div>
                      </div>
                      <!--input-group end-->

          </div>

      </div>

            {{ method_field('PATCH') }}
            {{ csrf_field() }}

            <div class="form-group">
              <label for="name">名称：</label>
              <input type="text" name="name" class="form-control" value="{{ $user->name }}">
            </div>

            <div class="form-group">
              <label for="email">邮箱：</label>
              <input type="text" name="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="form-group">
              <label for="password">密码：</label>
              <input type="password" name="password" class="form-control" value="{{ old('password') }}">
            </div>

            <div class="form-group">
              <label for="password_confirmation">确认密码：</label>
              <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
            </div>

            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
  </div>
</div>
@stop
<script>
  //图片上传预览    IE是用了滤镜。
    function previewImage(file)
    {
      var MAXWIDTH  = 90;
      var MAXHEIGHT = 90;
      var div = document.getElementById('preview');
      if (file.files && file.files[0])
      {
          div.innerHTML ='<img id=imghead onclick=$("#previewImg").click()>';
          var img = document.getElementById('imghead');
          img.onload = function(){
            var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
            img.width  =  rect.width;
            img.height =  rect.height;
//                 img.style.marginLeft = rect.left+'px';
            img.style.marginTop = rect.top+'px';
          }
          var reader = new FileReader();
          reader.onload = function(evt){img.src = evt.target.result;}
          reader.readAsDataURL(file.files[0]);
      }
      else //兼容IE
      {
        var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
        file.select();
        var src = document.selection.createRange().text;
        div.innerHTML = '<img id=imghead>';
        var img = document.getElementById('imghead');
        img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
        var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
        status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
        div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\"'></div>";
      }
    }
    function clacImgZoomParam( maxWidth, maxHeight, width, height ){
        var param = {top:0, left:0, width:width, height:height};
        if( width>maxWidth || height>maxHeight ){
            rateWidth = width / maxWidth;
            rateHeight = height / maxHeight;

            if( rateWidth > rateHeight ){
                param.width =  maxWidth;
                param.height = Math.round(height / rateWidth);
            }else{
                param.width = Math.round(width / rateHeight);
                param.height = maxHeight;
            }
        }
        param.left = Math.round((maxWidth - param.width) / 2);
        param.top = Math.round((maxHeight - param.height) / 2);
        return param;
    }
</script>
