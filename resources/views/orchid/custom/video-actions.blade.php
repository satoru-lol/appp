<a href="{{ route('platform.video.edit', $video->id) }}" class="btn btn-sm btn-info">
    Редактировать
</a>

<form method="POST" action="" onsubmit="return confirm('Вы действительно хотите удалить?');" style="display:inline;">
    @csrf
    <button type="submit"
            formaction="{{ route('platform.new_video') }}"
            name="method"
            value="deleteVideo"
            class="btn btn-sm btn-danger"
            data-params='{"id": {{ $video->id }}}'>
        Удалить
    </button>
</form>
