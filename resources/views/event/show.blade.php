@extends('layout.master') 
@section('content') @php $connected = false; 
if(session()->has('user')) { 
    $connected = true; 
    $user= App\user::find(session()->get('user')[0]); 
} 
    $creator = App\user::find($event->user_id); 
@endphp

<div class="parallax-container center valign-wrapper borderdown">
    <div class="parallax">
        <img src="/image/background.jpg" alt="parallax background">
    </div>
    <div class="container white-text">
        <div class="row">
            <div class="col s12">
                <h2>Évenement</h2>
            </div>
        </div>
    </div>
</div>

<br> {{-- Single event header with user profile and name of the event --}}
<section>
    {{-- Event header for tablet and desktop --}}
    <div class="row hide-on-small-only">
        <div class="col m2 l1 offset-l1 right-align">
            <img class="circle responsive-img profile-pic" src="/image/simon.jpg" alt="User's avatar">
        </div>
        <div class="col m2 l2">
            <p>{{$creator->first_name}} {{$creator->last_name}}</p>
        </div>
        <div class="col m7 l7 offset-l1">
            <h4>{{$event->name}}</h4>
        </div>
    </div>
    {{-- Event eader for mobile --}}
    <div class="row hide-on-med-and-up">
        <div class="col s2">
            <img class="circle responsive-img profile-pic" src="/image/simon.jpg" alt="User's avatar">
        </div>
        <div class="col s10">
            <p>{{$creator->first_name}} {{$creator->last_name}}</p>
        </div>
        <div class="col s12 center-align">
            <h4>{{$event->name}}</h4>
        </div>
    </div>
    <hr class="divider"> {{-- Description of the event --}}
    <div class="row">
        <div class="col s12 l10 offset-l1">
            <p>Description de l'eventment : {{$event->description}}</p>
            @if($event->recurency()) <p>Cet evenement à lieu tout  les {{$event->recurency()}} Jours</p>@endif
            <p>prix : {{ $event->price}} €</p>
        </div>
        <div class="col s12 center-align">
            {{-- Open a modal to add image if you were present on the event --}} 
            @if(($event->date<now() || $event->recurency()) && $connected && $event->statut)
                 @if($user->hasSubscribedToEvent($event->id))
                 <div class="col l12 m12 s12">
                     <a class="waves-effect waves-light btn modal-trigger" href="#modal1">
                        <i class="material-icons left">add_a_photo</i>
                        Publier une ou plusieurs photos de l'événement</a>
                 </div>
                 <div class="col l12 m12 s12"></div>
                    
                 @endif
            @elseif($connected && $event->statut)
                <form action="/event/{{$event->id}}/subscribe" method="POST">
                    @csrf
                    <button class="waves-effect waves-dark btn" type="submit">
                            @if ($user->hasSubscribedToEvent($event->id))
                                Se désinscrire
                            @else
                                S'inscrire
                            @endif
                            <i class="fas fa-sign-in-alt right"></i>
                            <div class="col l12 m12 s12"></div>
                    </button>
                </form>
            @elseif($event->statut)
                <a href="/login" class="waves-effect waves-dark btn">Connectez-vous pour interagir</a>
            @endif
            @if ($connected)
                @if($user->hasRole('Admin'))
                <div class="col l12 m12 s12">
                        <form action="/event/{{$event->id}}/pdf" method="GET">
                            @csrf
                            <button class="waves-effect waves-dark btn" type="submit">
                                Télécharger la liste des inscrits
                                <i class="fas fa-download right"></i>
                            </button>
                        </form>
                </div>
                        
                @endif
            @endif
               
            
                @if (count($errors) > 0)
                <div class="card-panel red lighten-5 login_waper">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <h6><li class="red-text">{{ $error }}</li></h6>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    {{-- Modal Structure to add an photo--}}
    <div id="modal1" class="modal">
        <div class="modal-content">
            <h4>Ajouter une ou plusieures photo(s)</h4>
            <br>
            <form action="/event/upload" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="event" value="{{$event->id}}">
                <div class="file-field input-field">
                    <div class="btn">
                        <span>File</span>
                        <input type="file" multiple name="images[]" accept="image/*">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Sélectionner une ou plusieurs photos">
                    </div>
                </div>
                <button class="modal-close waves-effect waves-green btn">Ajouter</button>
                <a href="#!" class="modal-close waves-effect waves-green btn">Annuler</a>
            </form>
        </div>
    </div>
</section>

<section>
    <div class="row">
        @foreach ($event->pictures as $picture)
        <div class="col s12 l6 center-align">
            <ul class="collapsible">
                <li>
                {{-- Collapside with comment --}}
                    <div class="collapsible-header">
                        <div class="row remove-marge-bot">
                            <div class="col s12 ">
                                <img class="circle responsive-img profile-pic" src="https://www.numerama.com/content/uploads/2018/05/slider-facebook-new-profile.jpg"
                                    alt="User's avatar">
                            </div>
                        </div>
                        <div class="col s12 left-align">
                            @php $pictureuser = App\user::find($picture->user_id) @endphp
                            <p>{{$pictureuser->first_name}} {{$pictureuser->last_name}}</p>
                        </div>
                    </div>
                    <div class="collapsible-header test">
                        <img class="materialboxed event-pic-show" src="/storage/{{$picture->link}}" alt="Comment picture">
                        @if ($connected)
                            {{-- Like button --}}
                            <form action="/picture/{{$picture->id}}/like" class="like">
                                <i   class="likebtn @if($user->haveLikedPicture($picture->id)) fas @else far @endif fa-heart" style="color:red"></i>
                            {{--End like button--}}
                            </form>
                            @if($user->hasRole('tutor'))
                                <a href="/picture/{{$picture->id}}/signal"><i class="signal fas fa-exclamation"></i></a>
                            @endif
                        @else
                            <p>Vous devez être connecté pour liker</p>
                        @endif
                        <div class="show-event">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        {{-- Input to write a comment --}} @if ($connected) {{-- Write a comment --}}
                        <form action="/comment" method="POST">
                            @csrf
                            <input type="hidden" name="picture" value="{{$picture->id}}">
                            <div class="row">
                                <div class="event-comment">
                                    <div class="card-panel grey lighten-5 z-depth-1">
                                        {{-- User actually conected profile --}}
                                        <div class="row remove-marge-bot">
                                            <div class="col s4 m2 l1">
                                                <img src="/image/simon.jpg" class="circle responsive-img" alt="User's avatar">
                                            </div>
                                            <div class="col s8 m10 l11">
                                                <div class="row">
                                                    <div class="s12 left">
                                                        <p>{{$user->first_name}} {{$user->last_name}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col s12 left">
                                                <div class="input-field">
                                                    <i class="fas fa-pen prefix"></i>
                                                    <textarea id="textarea1" class="materialize-textarea" data-length="120" name="comment"></textarea>
                                                    <label for="textarea1">Commentaire</label>
                                                </div>
                                                <div class="input-field">
                                                    <button class="btn">Commenter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif 
                        @foreach ($picture->comments as $comment) 
                            @php 
                                $writer =$comment->writer();
                            @endphp
                            <div class="row">
                                <div class="event-comment">
                                    <div class="card-panel grey lighten-5 z-depth-1">
                                        <div class="row">
                                            {{-- User's profile who comment in last --}}
                                            <div class="col s4 m2 l1">
                                                <img src="/image/simon.jpg" class="circle responsive-img" alt="User's avatar">
                                            </div>
                                            <div class="col s8 m10 l11">
                                                <div class="s12 left">
                                                    <div class="s12 left">
                                                        <p>{{$writer->first_name}} {{$writer->last_name}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Comment text --}}
                                            <div class="s12 left">
                                                <p class="comment left"> {{$comment->content}}</p>
                                            </div>
                                        </div>
                                        <div class="row remove-marge-bot">
                                            <div class="col s6 left-align">
                                                {{-- Date --}} {{$comment->created_at}}
                                            </div>
                                        <div class="col s6 right-align"> 
                                            @if($connected)
                                                @if($user->hasRole('tutor'))
                                                    <a class="waves-effect waves-light btn" href="/comment/{{$comment->id}}/signal"><i class="fas fa-exclamation-triangle"></i></a>
                                                @endif
                                                @if($user->hasRole('admin'))
                                                    <a class="waves-effect waves-light btn" href="/comment/{{$comment->id}}/delete"><i class="fas fa-ban"></i></a>
                                                @endif 
                                            @endif        
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach           
                    </div>
                </li>
            </ul>
        </div>
        @endforeach
    </div>
</section>

@endsection
 
@section('scripts')
<script>
    $(document).ready(function(){
        $('.materialboxed').materialbox();
        $('input#input_text, textarea#textarea1').characterCounter();
        var bool = true;
        //Ajax request to like the picture
        $(".likebtn").click(function(){

            $(this).parent().trigger('submit')
            $(this).toggleClass('far')
            $(this).toggleClass('fas')
            bool =$(this).hasClass('fas')

        })

        $(".like").each(function(){
            $(this).submit(function(e) {
                e.preventDefault(); // avoid to execute the actual submit of the
                var form = $(this);

                $.ajax({ type: form.attr('method'), url: form.attr('action'),
                data: form.serialize(), success: function (data) {
                    console.log(data);

                    if(bool){

                        M.toast({html:"Vous etes le "+ data +"e a avoir liké"})
                    }


                    },
                    error: function (data) {
                        console.log('An error occurred.');
                        },
                    });
            })
        });




    });

</script>
@endsection