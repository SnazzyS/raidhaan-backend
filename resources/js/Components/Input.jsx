export default function Input({
    label,
    error,
    className = '',
    ...props
}) {
    return (
        <div className={className}>
            {label && (
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    {label}
                </label>
            )}
            <input
                className={`
                    w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                    placeholder:text-gray-400 focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200
                    ${error ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100' : ''}
                `}
                {...props}
            />
            {error && (
                <p className="mt-1 text-xs text-rose-600">{error}</p>
            )}
        </div>
    );
}
