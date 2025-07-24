{{--
@extends('platform::layouts.app')
--}}

@section('content')
    <!-- Ваш HTML-код для экрана -->
    @include('platform.selects')
@endsection
<input type="hidden" name="_state" id="screen-state" value="">
@push('scripts')
    <script>

        start();
        var menuItems = document.querySelectorAll('.active');

        // Добавить обработчик события клика ко всем элементам меню
        menuItems.forEach(function(item) {
            item.addEventListener('click', function(event) {
                event.preventDefault(); // Предотвратить действие по умолчанию (если это ссылка)

                // Получить действие из атрибута data-action
                //var action = item.getAttribute('data-action');

                // Ваш код для обработки действий
                start();
            });
        });

        function start() {
            const select1 = document.querySelector('select[name="option1"]');
            const select2 = document.querySelector('select[name="option2"]');
            const select2Row = select2.closest('.row');

            fetch(`/api/getParts`)
                .then(response => response.json())
                .then(data => {
                    debugger;
                    select1.innerHTML = ''; // Clear existing options
                    for (const [value, text] of Object.entries(data)) {
                        const option = document.createElement('option');
                        option.value = text.id;
                        option.textContent = text.value;
                        select1.appendChild(option);
                    }

                    select1.style.display = 'block';
                })
                .catch(error => console.error('Error fetching options:', error));
            fetchData("1", select2, select2Row)

            select1.addEventListener('change', function() {
                const selectedValue = this.value;
                debugger;
                if (selectedValue) {
                    fetchData(selectedValue, select2, select2Row)
                } else {
                    select2Row.style.display = 'none';
                }
            });


        }
        function fetchData(selectedValue, select2, select2Row)
        {
            fetch(`/api/select-options?option1=${selectedValue}`)
                .then(response => response.json())
                .then(data => {
                    select2.removeAttribute("hidden")
                    select2.innerHTML = ''; // Clear existing options
                    for (const [value, text] of Object.entries(data)) {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = text;
                        select2.appendChild(option);
                    }

                    select2Row.style.display = 'block';
                })
                .catch(error => console.error('Error fetching options:', error));
        }

    </script>
@endpush
