<template>
    <div style="display: flex; justify-content: space-around; width: 100%;">
        <div id="on" :class="{active: value == true}" @click="switched(true)"><i class="far fa-check-circle"></i></div>
        <div id="off" :class="{active: value == false}" @click="switched(false)"><i class="fas fa-times"></i></div>
    </div>
</template>

<script>
    export default {
        name: "TrueFalseSwitch",
        props: ['value'],
        methods: {
            switched(isOn) {
                this.$emit('input', isOn);
            }
        }
    }
</script>

<style scoped>
    .active {
        color: green;
    }
</style>