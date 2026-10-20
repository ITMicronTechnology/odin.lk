@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Odin.lk')
{{-- Text wordmark: email clients such as Gmail block SVG logos and localhost images. --}}
<span style="color: #e10600; letter-spacing: -0.5px;">Odin</span><span style="color: #0a3d91;">.lk</span>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
