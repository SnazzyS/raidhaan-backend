export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center p-4 text-gray-900">
            <div className="w-full max-w-md">
                <div className="text-center mb-6">
                    <h1 className="text-xl font-semibold text-gray-900">Raidhaan</h1>
                </div>
                <div className="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    {children}
                </div>
            </div>
        </div>
    );
}
