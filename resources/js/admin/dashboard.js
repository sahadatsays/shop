import ApexCharts from 'apexcharts';
import Alpine from 'alpinejs';

const chartColors = ['#b08968', '#1c2941', '#517b69', '#4f6485', '#9a7355', '#3a4c6b'];

const isDark = () => document.documentElement.classList.contains('dark');

const baseChartOptions = () => {
    const dark = isDark();

    return {
        chart: {
            fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 600,
            },
            background: 'transparent',
        },
        theme: { mode: dark ? 'dark' : 'light' },
        grid: {
            borderColor: dark ? '#1f2937' : '#e2e8f0',
            strokeDashArray: 4,
        },
        xaxis: {
            labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } },
        },
        tooltip: { theme: dark ? 'dark' : 'light' },
        colors: chartColors,
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
    };
};

const buildOptions = (chart) => {
    const base = baseChartOptions();

    if (chart.type === 'donut') {
        return {
            ...base,
            chart: { ...base.chart, type: 'donut' },
            labels: chart.labels,
            series: chart.series[0]?.data ?? [],
            legend: {
                position: 'bottom',
                labels: { colors: isDark() ? '#cbd5e1' : '#475569' },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: chart.title,
                                color: isDark() ? '#f1f5f9' : '#0f172a',
                            },
                        },
                    },
                },
            },
        };
    }

    return {
        ...base,
        chart: { ...base.chart, type: chart.type === 'area' ? 'area' : chart.type },
        series: chart.series,
        xaxis: { ...base.xaxis, categories: chart.labels },
        fill: chart.type === 'area'
            ? {
                  type: 'gradient',
                  gradient: {
                      shadeIntensity: 0.3,
                      opacityFrom: 0.45,
                      opacityTo: 0.05,
                      stops: [0, 90, 100],
                  },
              }
            : undefined,
    };
};

export const registerDashboardAlpine = () => {
    Alpine.data('dashboardCharts', () => ({
        charts: [],
        instances: [],

        init(payload) {
            this.charts = Array.isArray(payload) ? payload : [];
            this.$nextTick(() => this.renderCharts());
        },

        renderCharts() {
            this.destroyCharts();

            this.charts.forEach((chart) => {
                const element = this.$refs[chart.id];

                if (!element) {
                    return;
                }

                const instance = new ApexCharts(element, buildOptions(chart));
                instance.render();
                this.instances.push(instance);
            });
        },

        destroyCharts() {
            this.instances.forEach((instance) => instance.destroy());
            this.instances = [];
        },
    }));

    Alpine.data('dashboardQuickAction', () => ({
        run(action) {
            if (action.href) {
                window.location.href = action.href;
                return;
            }

            window.adminToast?.push({
                title: action.label,
                message: action.description ?? 'This module is coming soon.',
                type: 'info',
            });
        },
    }));
};

export { Alpine };
