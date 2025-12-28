const variants = {
    default: 'bg-gray-100 text-gray-700',
    primary: 'bg-gray-100 text-gray-700',
    success: 'bg-emerald-50 text-emerald-700',
    warning: 'bg-amber-50 text-amber-700',
    danger: 'bg-rose-50 text-rose-700',
    info: 'bg-sky-50 text-sky-700',
};

export default function Badge({ children, variant = 'default', className = '' }) {
    return (
        <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${variants[variant]} ${className}`}>
            {children}
        </span>
    );
}

function StatusBadge({ status }) {
    const statusMap = {
        pending: { variant: 'warning', label: 'Pending' },
        completed: { variant: 'success', label: 'Completed' },
        cancelled: { variant: 'danger', label: 'Cancelled' },
        processing: { variant: 'info', label: 'Processing' },
    };

    const config = statusMap[status] || { variant: 'default', label: status };
    return <Badge variant={config.variant}>{config.label}</Badge>;
}

Badge.Status = StatusBadge;
