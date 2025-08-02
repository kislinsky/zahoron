@extends('account.user.components.page')
@section('title', "Кошельки")

@section('content')

    <div class="ul_wallets">
        @foreach ($wallets as $key=>$wallet)
            <div class="li_wallet">
                <div class="text_middle_index">Кошелек №{{ $key }}</div>
                <div class="text_middle_index">Баланс: {{ $wallet->balance }}</div>

                <form class='block_input' action="{{ route('account.user.wallet.update.balance') }}" method="post">
                    @csrf
                    <input type="hidden" name="wallet_id" value={{ $wallet->id }}>
                    <input type="number" name="count" id="" value=3000>
                    <button class='blue_btn'>Пополнить</button>
                </form>
                <form action="{{ route('account.user.wallet.delete',$wallet->id) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class='delete_cart'><img src="{{asset('storage/uploads/Trash.svg')}}" alt="">Удалить кошелек</button>
                </form>
            </div>    
        @endforeach
        
    </div>

@endsection