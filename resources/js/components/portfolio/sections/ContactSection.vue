<script setup>
import { reactive, ref } from 'vue';

const props = defineProps({
    contacts: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    submitHandler: {
        type: Function,
        required: true,
    },
});

const form = reactive({
    name: '',
    email: '',
    subject: '',
    message: '',
    company_website: '',
});

const submitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const onSubmit = async () => {
    if (submitting.value) return;

    submitting.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        await props.submitHandler({ ...form });
        successMessage.value = 'پیام شما با موفقیت ثبت شد.';
        form.name = '';
        form.email = '';
        form.subject = '';
        form.message = '';
        form.company_website = '';
    } catch (error) {
        errorMessage.value = error?.message || 'ارسال پیام ناموفق بود.';
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <section class="section" id="contact">
        <h2>تماس با من</h2>
        <div class="underline"></div>

        <p v-if="contacts.description" class="text-block">{{ contacts.description }}</p>

        <div v-if="loading" class="panel">
            <p class="text-block">در حال بارگذاری اطلاعات تماس...</p>
        </div>

        <template v-else>
            <div v-if="successMessage" class="form-alert form-alert-success is-visible">{{ successMessage }}</div>
            <div v-if="errorMessage" class="form-alert form-alert-error is-visible">{{ errorMessage }}</div>

            <h2 style="font-size: clamp(24px, 2vw, 30px); margin-bottom: 12px;">فرم تماس</h2>
            <form id="contactForm" class="contact-form" @submit.prevent="onSubmit">
                <input v-model="form.name" type="text" name="name" placeholder="نام و نام خانوادگی" required>
                <input v-model="form.email" type="email" name="email" placeholder="آدرس ایمیل" dir="ltr" style="text-align:left;" required>
                <input v-model="form.subject" type="text" name="subject" class="full" placeholder="موضوع" required>
                <textarea v-model="form.message" name="message" class="full" placeholder="پیام شما" required></textarea>
                <input
                    v-model="form.company_website"
                    type="text"
                    name="company_website"
                    tabindex="-1"
                    autocomplete="off"
                    aria-hidden="true"
                    style="position:absolute;left:-9999px;opacity:0;height:0;width:0;pointer-events:none;">
                <button class="submit" type="submit" :disabled="submitting">
                    {{ submitting ? 'در حال ارسال...' : 'ارسال پیام' }}
                </button>
            </form>
        </template>
    </section>
</template>
