const variants = {
    primary: 'bg-gray-900 text-white shadow-sm hover:bg-gray-800 focus-visible:outline-gray-900',
    secondary: 'bg-white text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 focus-visible:outline-gray-400',
    success: 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 focus-visible:outline-emerald-600',
    danger: 'bg-rose-600 text-white shadow-sm hover:bg-rose-700 focus-visible:outline-rose-600',
    ghost: 'text-gray-600 hover:text-gray-900 hover:bg-gray-100',
};

const sizes = {
    sm: 'px-2.5 py-1.5 text-xs',
    md: 'px-3 py-2 text-sm',
    lg: 'px-4 py-2.5 text-base',
};

export default function Button({
    children,
    variant = 'primary',
    size = 'md',
    className = '',
    disabled = false,
    ...props
}) {
    return (
        <button
            className={`
                inline-flex items-center justify-center gap-2 rounded-md border border-transparent font-medium
                transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                disabled:opacity-50 disabled:cursor-not-allowed
                ${variants[variant]}
                ${sizes[size]}
                ${className}
            `}
            disabled={disabled}
            {...props}
        >
            {children}
        </button>
    );
}
