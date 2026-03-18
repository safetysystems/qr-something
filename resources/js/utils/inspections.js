export function inspectionStatusLabel(status) {
    const labels = {
        passed: 'Passed',
        failed: 'Failed',
        not_applicable: 'Not applicable',
    };

    return labels[status] || 'Unknown';
}

export function inspectionStatusClass(status) {
    const classes = {
        passed: 'status-pill status-pill-passed',
        failed: 'status-pill status-pill-failed',
        not_applicable: 'status-pill status-pill-neutral',
    };

    return classes[status] || 'status-pill status-pill-neutral';
}
