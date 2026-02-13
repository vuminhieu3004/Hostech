import ServiceList from "../../../Components/Service/ServiceList";
import ServiceForm from "../../../Components/Service/ServiceForm";
import ServiceCalculator from "../../../Components/Service/ServiceCalculator";

const Services = () => {
    return (
        <div className="p-4 flex gap-6 flex-col lg:flex-row">
            <div className="flex-1">
                <ServiceList />
                <ServiceForm />
            </div>
            <div className="w-full lg:w-1/3">
                <ServiceCalculator />
            </div>
        </div>
    );
};

export default Services;
