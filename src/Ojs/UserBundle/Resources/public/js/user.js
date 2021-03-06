$(document).ready(function () {
    $.fn.usersType = function(action) {
        if(action){
            this.select2(action);
            return this;
        }
        this.select2({
            ajax: {
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            templateResult: function (user) {
                if (user.loading) {
                    return user.text;
                }
                return user.username + ' ( ' + user.first_name + ' ' + user.last_name + ' ~ ' + user.email + ' )';
            },
            templateSelection: function (user) {
                if(!user.username) {
                    return user.text;
                }
                return user.username + ' ( ' + user.first_name + ' ' + user.last_name + ' ~ ' + user.email + ' )';
            }
        });
        return this;
    };

    $('.usersType').usersType();
});
