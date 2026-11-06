import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
};

export default function Login({ status, canResetPassword }: Props) {
    return (
        <>
            <Head title="Ingresar" />

            <Form
                {...store.form()}
                resetOnSuccess={['password']}
                className="flex flex-col gap-7"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label
                                    htmlFor="email"
                                    className="text-sm font-semibold text-[#6B2D3C]"
                                >
                                    Correo electrónico
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="email"
                                    placeholder="admin@gmail.com"
                                    className="h-14 rounded-2xl border-[#6B2D3C]/15 bg-[#FBF5EE] px-4 text-base text-[#3B2A2A] placeholder:text-[#3B2A2A]/40 focus-visible:border-[#C98B8B] focus-visible:ring-[#C98B8B]/30"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <div className="flex items-center">
                                    <Label
                                        htmlFor="password"
                                        className="text-sm font-semibold text-[#6B2D3C]"
                                    >
                                        Contraseña
                                    </Label>
                                    {canResetPassword && (
                                        <TextLink
                                            href={request()}
                                            className="ml-auto text-sm font-semibold text-[#C98B8B] hover:text-[#6B2D3C]"
                                            tabIndex={5}
                                        >
                                            ¿Olvidaste tu contraseña?
                                        </TextLink>
                                    )}
                                </div>
                                <PasswordInput
                                    id="password"
                                    name="password"
                                    required
                                    tabIndex={2}
                                    autoComplete="current-password"
                                    placeholder="Ingresá tu contraseña"
                                    className="h-14 rounded-2xl border-[#6B2D3C]/15 bg-[#FBF5EE] px-4 text-base text-[#3B2A2A] placeholder:text-[#3B2A2A]/40 focus-visible:border-[#C98B8B] focus-visible:ring-[#C98B8B]/30"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex items-center gap-3">
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    tabIndex={3}
                                />
                                <Label
                                    htmlFor="remember"
                                    className="text-sm text-[#3B2A2A]/65"
                                >
                                    Recordarme en este dispositivo
                                </Label>
                            </div>

                            <Button
                                type="submit"
                                className="mt-3 h-14 w-full rounded-full bg-[#6B2D3C] text-base font-bold text-white shadow-none hover:bg-[#3B2A2A]"
                                tabIndex={4}
                                disabled={processing}
                                data-test="login-button"
                            >
                                {processing && <Spinner />}
                                Ingresar al panel
                            </Button>
                        </div>
                        <p className="mt-5 text-center text-sm leading-6 text-[#3B2A2A]/55">
                            Acceso de gestión para la demo de Delicias Sn.
                        </p>
                    </>
                )}
            </Form>

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </>
    );
}

Login.layout = {
    title: 'Bienvenida al panel',
    description: 'Ingresá para gestionar tu pastelería',
};
