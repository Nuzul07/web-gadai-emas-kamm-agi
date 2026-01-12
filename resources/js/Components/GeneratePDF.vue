<template>
    <div>
        <button @click="generatePDF">Generate PDF</button>
        <button v-if="pdfSrc" @click="downloadPDF">Download PDF</button>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const pdfSrc = ref(null);

const generatePDF = async () => {
    try {
        const response = await axios.get("/gadaiemas-&-dashboard/generate-pdf");
        const base64pdf = response.data.base64pdf;
        pdfSrc.value = `data:application/pdf;base64,${base64pdf}`;

        // Open PDF in a new tab
        const pdfWindow = window.open("");
        pdfWindow.document.write(
            `<iframe width='100%' height='100%' src='data:application/pdf;base64,${base64pdf}'></iframe>`
        );
    } catch (error) {
        console.error("Error generating PDF:", error);
    }
};

const downloadPDF = () => {
    const link = document.createElement("a");
    link.href = pdfSrc.value;
    link.download = "document.pdf";
    link.click();
};
</script>

<style>
/* Add any styles you need */
</style>
