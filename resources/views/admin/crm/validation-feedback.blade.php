@if($errors->any())
<section class="notice" role="alert" tabindex="-1" style="border-color:#f4c7ce;background:#fff4f5;color:var(--admin-primary-dark)">
    <h2 style="font-size:15px;margin:0 0 8px">Check these details</h2>
    <ul style="margin:0;padding-left:20px">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul>
    <p style="margin:8px 0 0">Your changes were not saved. Correct the fields and try again. File inputs must be selected again.</p>
</section>
@endif
