@extends('app', [
'title' => 'Заявка вступления в полигон',
'keywords' => '', # Ключевые слова
'description' => '' # Описание страницы
])

@section('content')
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <li>Заявка вступления в полигон</li>
            </ul>
        </div>
    </div>
    <style>
        .card {
            border: none;
            transition: transform 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
            overflow: hidden;
            border-radius: 15px;
        }
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
        .card-header {
            background: linear-gradient(to right, #613482, #8a2c77);
            color: #fff;
            text-align: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        .card-header::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }
        .card-header:hover::before {
            opacity: 1;
        }
        .card-body {
            background-color: #f8f9fa;
            padding: 1.5rem;
            position: relative;
        }
        .list-group-item {
            transition: background-color 0.3s ease, transform 0.3s ease;
            border: none;
            padding: 1rem;
        }
        .list-group-item:hover {
            background-color: #e9ecef;
            transform: translateX(10px);
        }
        .list-group-item span {
            display: inline-block;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .list-group-item span:hover {
            color: #613482;
            transform: scale(1.05);
        }
        .btn-custom {
            background-color: #613482;
            color: white;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #8a2c77;
            transform: scale(1.05);
        }

        /* Responsive styles */
        @media (max-width: 767.98px) {
            .card {
                width: 100%;
                margin: 10px 0;
            }
        }
        @media (min-width: 768px) and (max-width: 991.98px) {
            .card {
                width: 90%;
                margin: 20px auto;
            }
        }
        @media (min-width: 992px) {
            .card {
                width: 100%;
                max-width: 700px;
                margin: 30px auto;
            }
        }

    </style>
    <div class="container-xl px-4 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10 col-xl-8">
                <div class="card shadow-lg">
                    <div class="card-header">
                        <h4 class="mb-0">Информация</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center border-bottom">
                                <span class="font-weight-bold text-muted">Имя:</span>
                                <span class="font-weight-normal">{{$info->firstname}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-bottom">
                                <span class="font-weight-bold text-muted">Фамилия:</span>
                                <span class="font-weight-normal">{{$info->lastname}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-bottom">
                                <span class="font-weight-bold text-muted">Эл. почта:</span>
                                <span class="font-weight-normal">{{$info->email}}</span>
                            </li>
                        </ul>
                        <button class="btn btn-custom mt-3">Подтвердить участие</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('js/bootstrap.bundle.js')}}"></script>
{{--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>--}}
    <br><br>
@endsection
