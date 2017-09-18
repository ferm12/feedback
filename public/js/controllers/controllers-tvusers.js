// new App.Router;

Backbone.history.start();

App.clients = new App.Collections.Clients;
App.clients.fetch().then(function(){
    new App.Views.App({collection: App.clients});
});

$('#add-user-btn').on('click', function(e){
    backboneEvent.trigger('client:add');
    $(this).toggle();
});
//
// $("#see-more-less").on("click", function(event) {
//     $('#client').slideToggle('slow');
//     $('#add-client-form').slideToggle('slow');
//     $('#minus').toggle();
//     $('#plus').toggle();
// });	
//
// $('#see-more-less').trigger('click');


// $('#add-client-form').on('submit', function(e){
//     e.preventDefault();
//     var email_address = $('#email_address'),
//         password = $('#password').val(),
//         username = email_address.val().split('@');

//         
//     $.ajax({
//         url:'clients',
//         type: 'POST',
//         data: {
//             activated: 1,
//             company_id: 0,
//             email: email_address.val(),
//             username: username[0],
//             password: password,
//             password_unencrypted: password,
//             first_name: '',
//             last_name: '',
//             video_id: $('#video_id').val()
//         },:w
//
//         success: function(response){
//             console.log(response);
//             console.log('You added a user');
//             var added_user_msg = $('<div/>');
//             added_user_msg.text('You have successfully added '+username[0]+' to '+ $('#video_title').text()+' video!');
//             added_user_msg.addClass('added_user_smg');
//             setTimeout(function(){
//                 added_user_msg.slideUp({
//                     duration: 'slow',
//               complete: function(){
//                         $(this).remove();
//                         email_address.val('');
//                         password.val('');
//                     }
//                 });
//             },3000);
//             $('#page-wrapper').before(added_user_msg);
//             $('#see-more-less').trigger('click');
//         },
//         complete: function(xhr, textStatus){
//             console.log(textStatus);
//         },
//         error: function(jqXHR, textStatus, errorThrown){
//             console.log(errorThrown);
//         }
//     });
// });
