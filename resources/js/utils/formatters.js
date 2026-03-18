export function formatDate(value) {
    if (!value) {
        return 'Not set';
    }

    return new Intl.DateTimeFormat('en', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
    }).format(new Date(value));
}

export function formatDateTime(value) {
    if (!value) {
        return 'Not set';
    }

    return new Intl.DateTimeFormat('en', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

export function compactAddress(address) {
    if (!address) {
        return 'Not set';
    }

    return [
        address.line_1,
        address.line_2,
        address.city,
        address.state,
        address.postal_code,
        address.country,
    ]
        .filter(Boolean)
        .join(', ') || 'Not set';
}
