// Dashboard Charts Configuration
document.addEventListener('DOMContentLoaded', function() {
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }

    // Get chart contexts
    const monthlyActivityCtx = document.getElementById('monthlyActivityChart');
    const departmentCtx = document.getElementById('departmentChart');

    if (!monthlyActivityCtx || !departmentCtx) {
        console.error('Chart canvases not found');
        return;
    }

    // Chart.js default configuration for dark mode support
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // Function to get current theme colors
    function getThemeColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            textColor: isDark ? '#f9fafb' : '#374151',
            gridColor: isDark ? '#374151' : '#e5e7eb',
            backgroundColor: isDark ? '#1f2937' : '#ffffff'
        };
    }

    // Monthly Hospital Activity Chart (Bar Chart)
    const monthlyActivityChart = new Chart(monthlyActivityCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Patients',
                    data: [240, 270, 310, 280, 340, 360],
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Appointments',
                    data: [120, 140, 150, 160, 180, 190],
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Procedures',
                    data: [40, 50, 45, 55, 60, 65],
                    backgroundColor: '#f59e0b',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: getThemeColors().textColor,
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                x: {
                    grid: {
                        color: getThemeColors().gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: getThemeColors().textColor
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: getThemeColors().gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: getThemeColors().textColor
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Patient Distribution by Department Chart (Pie Chart)
    const departmentChart = new Chart(departmentCtx, {
        type: 'pie',
        data: {
            labels: ['Emergency', 'Cardiology', 'Surgery', 'Pediatrics', 'Others'],
            datasets: [{
                data: [35, 25, 15, 20, 5],
                backgroundColor: [
                    '#ef4444', // Emergency - Red
                    '#3b82f6', // Cardiology - Blue
                    '#f59e0b', // Surgery - Orange
                    '#10b981', // Pediatrics - Green
                    '#8b5cf6'  // Others - Purple
                ],
                borderColor: [
                    '#dc2626',
                    '#2563eb',
                    '#d97706',
                    '#059669',
                    '#7c3aed'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: getThemeColors().textColor,
                        usePointStyle: true,
                        padding: 20,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const dataset = data.datasets[0];
                                    const value = dataset.data[i];
                                    const total = dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    
                                    return {
                                        text: `${label} ${percentage}%`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor[i],
                                        lineWidth: dataset.borderWidth,
                                        pointStyle: 'circle',
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${percentage}%`;
                        }
                    }
                }
            }
        }
    });

    // Function to update chart colors when theme changes
    function updateChartColors() {
        const colors = getThemeColors();
        
        // Update Monthly Activity Chart
        monthlyActivityChart.options.plugins.legend.labels.color = colors.textColor;
        monthlyActivityChart.options.scales.x.grid.color = colors.gridColor;
        monthlyActivityChart.options.scales.x.ticks.color = colors.textColor;
        monthlyActivityChart.options.scales.y.grid.color = colors.gridColor;
        monthlyActivityChart.options.scales.y.ticks.color = colors.textColor;
        monthlyActivityChart.update('none');
        
        // Update Department Chart
        departmentChart.options.plugins.legend.labels.color = colors.textColor;
        departmentChart.update('none');
    }

    // Listen for theme changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateChartColors();
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });

    // Resize charts when window is resized
    window.addEventListener('resize', function() {
        monthlyActivityChart.resize();
        departmentChart.resize();
    });
});
