<template>
    <div>{{ msg }} - {{ usersCount }}</div>
</template>
<script>
    export default{
    
        data(){
            return {
                msg: 'message',
                usersCount: 0,
            }
        },
        mounted(){
            console.log('Component mounted.');
            window.Echo.channel('trades')
            .listen('NewTrade', (e) => {
                console.log(e.trade[0][0]);
                this.msg = e.trade[0][0];
            });

            let channel = window.Echo.join(`presence.2702`)

            channel
                .here((users) => {
                    console.log("here: ",users);
                })
                .joining((user) => {
                    console.log("joining: ",user);
                })
                .leaving((user) => {
                    console.log("leaving: ",user);
                })
                .error((error) => {
                    console.log("error: ",error);
                });
        }
    }
</script>